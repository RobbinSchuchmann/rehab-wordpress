import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextControl, TextareaControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

const EMPTY_JOB = { imageUrl: '', imageAlt: '', eyebrow: '', title: '', body: '', applyText: '', applyUrl: '' };

// Split a plain \n\n-separated body into paragraphs for the editor preview.
const toParagraphs = ( body ) => ( body || '' ).split( /\n\s*\n/ ).map( ( s ) => s.trim() ).filter( ( s ) => s !== '' );

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const jobs = a.jobs || [];
		const setJob = ( i, key ) => ( v ) => {
			const next = jobs.map( ( j, idx ) => ( idx === i ? { ...j, [ key ]: v } : j ) );
			setAttributes( { jobs: next } );
		};
		const aspectClass = a.imageAspect === 'wide' ? ' rehab-article-row__media--wide' : '';
		return (
			<>
				<InspectorControls>
					<PanelBody title="Section" initialOpen>
						<SelectControl
							label="Heading background"
							value={ a.background }
							options={ [ { label: 'Cream', value: 'cream' }, { label: 'White', value: 'white' }, { label: 'Sage mist', value: 'sage-mist' } ] }
							onChange={ set( 'background' ) }
						/>
						<SelectControl
							label="Image aspect"
							value={ a.imageAspect }
							options={ [ { label: 'Wide (5/4)', value: 'wide' }, { label: 'Tall (4/5)', value: 'tall' } ] }
							onChange={ set( 'imageAspect' ) }
						/>
					</PanelBody>
					{ jobs.map( ( job, i ) => (
						<PanelBody key={ i } title={ `Job ${ i + 1 } — ${ job.title || 'untitled' }` } initialOpen={ false }>
							<MediaUploadCheck>
								<MediaUpload
									onSelect={ ( m ) => {
										const next = jobs.map( ( j, idx ) => ( idx === i ? { ...j, imageUrl: m.url, imageAlt: m.alt || j.imageAlt } : j ) );
										setAttributes( { jobs: next } );
									} }
									allowedTypes={ [ 'image' ] }
									render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ job.imageUrl ? 'Replace image' : 'Pick image' }</Button> }
								/>
							</MediaUploadCheck>
							<TextControl label="Image URL" value={ job.imageUrl } onChange={ setJob( i, 'imageUrl' ) } />
							<TextControl label="Image alt" value={ job.imageAlt } onChange={ setJob( i, 'imageAlt' ) } />
							<TextControl label="Eyebrow (small label)" value={ job.eyebrow } onChange={ setJob( i, 'eyebrow' ) } />
							<TextControl label="Job title" value={ job.title } onChange={ setJob( i, 'title' ) } />
							<TextareaControl
								label="Description"
								help="Separate paragraphs with a blank line."
								value={ job.body }
								onChange={ setJob( i, 'body' ) }
								rows={ 6 }
							/>
							<TextControl label="Apply button text (optional)" value={ job.applyText } onChange={ setJob( i, 'applyText' ) } />
							<TextControl label="Apply button URL (optional)" value={ job.applyUrl } onChange={ setJob( i, 'applyUrl' ) } />
							<Button isDestructive variant="secondary" onClick={ () => setAttributes( { jobs: jobs.filter( ( _, j ) => j !== i ) } ) }>Remove job</Button>
						</PanelBody>
					) ) }
					<PanelBody title="Jobs" initialOpen={ jobs.length === 0 }>
						<Button variant="primary" onClick={ () => setAttributes( { jobs: [ ...jobs, { ...EMPTY_JOB } ] } ) }>Add job</Button>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					{ ( a.eyebrow || a.heading || a.lede ) ? (
						<section className={ `rehab-job-listings__head-section rehab-bg-${ a.background }` }>
							<div className="rehab-container">
								<div className="rehab-job-listings__head">
									<RichText tagName="span" className="rehab-job-listings__eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
									<RichText tagName="h2" className="rehab-job-listings__heading" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Section heading…" allowedFormats={ [] } />
									<RichText tagName="p" className="rehab-job-listings__lede" value={ a.lede } onChange={ set( 'lede' ) } placeholder="Intro line (optional)…" allowedFormats={ [] } />
								</div>
							</div>
						</section>
					) : null }
					{ jobs.map( ( job, i ) => (
						<section key={ i } className={ `rehab-article-row-section rehab-bg-${ i % 2 === 0 ? 'white' : 'cream' }` }>
							<div className="rehab-container">
								<div className="rehab-article-row">
									<div className={ `rehab-article-row__media${ aspectClass }` }>
										{ job.imageUrl ? <img src={ job.imageUrl } alt={ job.imageAlt } /> : <div className="rehab-article-row__media-placeholder"><span>{ job.imageAlt || 'Image' }</span></div> }
									</div>
									<div className="rehab-article-row__text">
										{ job.eyebrow ? <span className="rehab-article-row__eyebrow">{ job.eyebrow }</span> : null }
										<h3 className="rehab-article-row__heading">{ job.title || 'Untitled role' }</h3>
										{ toParagraphs( job.body ).map( ( p, j ) => <p key={ j }>{ p }</p> ) }
										{ job.applyText && job.applyUrl ? (
											<div className="rehab-article-row__cta"><span className="rehab-btn rehab-btn--luxury">{ job.applyText }</span></div>
										) : null }
									</div>
								</div>
							</div>
						</section>
					) ) }
					{ jobs.length === 0 ? (
						<section className="rehab-article-row-section rehab-bg-white">
							<div className="rehab-container"><p>No jobs yet — add one from the “Jobs” panel on the right.</p></div>
						</section>
					) : null }
				</div>
			</>
		);
	},
	save: () => null,
} );
