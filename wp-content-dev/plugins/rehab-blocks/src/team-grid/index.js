import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextControl, TextareaControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

const EMPTY_MEMBER = { cat: 'support', name: '', role: '', excerpt: '', photoUrl: '', photoAlt: '', url: '' };

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const members = a.members || [];
		const cats = ( a.filters || [] ).filter( ( f ) => f.cat !== 'all' );
		const setMember = ( i, key ) => ( v ) => {
			const next = members.map( ( m, idx ) => ( idx === i ? { ...m, [ key ]: v } : m ) );
			setAttributes( { members: next } );
		};
		return (
			<>
				<InspectorControls>
					{ members.map( ( m, i ) => (
						<PanelBody key={ i } title={ `${ i + 1 }. ${ m.name || 'New member' }` } initialOpen={ false }>
							<TextControl label="Name" value={ m.name } onChange={ setMember( i, 'name' ) } />
							<TextControl label="Role" value={ m.role } onChange={ setMember( i, 'role' ) } />
							<SelectControl label="Discipline" value={ m.cat } options={ cats.map( ( c ) => ( { label: c.label, value: c.cat } ) ) } onChange={ setMember( i, 'cat' ) } />
							<TextareaControl label="Excerpt" value={ m.excerpt } onChange={ setMember( i, 'excerpt' ) } rows={ 3 } />
							<TextControl label="Photo URL" value={ m.photoUrl } onChange={ setMember( i, 'photoUrl' ) } />
							<TextControl label="Profile URL" value={ m.url } onChange={ setMember( i, 'url' ) } />
							<Button isDestructive variant="secondary" onClick={ () => setAttributes( { members: members.filter( ( _, j ) => j !== i ) } ) }>Remove member</Button>
						</PanelBody>
					) ) }
					<PanelBody title="Members" initialOpen={ false }>
						<Button variant="primary" onClick={ () => setAttributes( { members: [ ...members, { ...EMPTY_MEMBER } ] } ) }>Add member</Button>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className={ `rehab-team-grid rehab-bg-${ a.background }` }>
						<div className="rehab-container">
							<div className="rehab-team-grid__head">
								<RichText tagName="span" className="rehab-team-grid__eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
								<RichText tagName="h2" className="rehab-team-grid__heading" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />
								<RichText tagName="p" className="rehab-team-grid__lede" value={ a.lede } onChange={ set( 'lede' ) } placeholder="Lede…" allowedFormats={ [] } />
							</div>
							<div className="rehab-team-grid__filter">
								{ ( a.filters || [] ).map( ( f, i ) => <button key={ i } type="button" className={ i === 0 ? 'on' : '' }>{ f.label }</button> ) }
							</div>
							<div className="rehab-team-grid__grid">
								{ members.map( ( m, i ) => (
									<div className="rehab-team-card" key={ i }>
										<div className="rehab-team-card__photo">{ m.photoUrl ? <img src={ m.photoUrl } alt={ m.name } /> : null }</div>
										<div className="rehab-team-card__head"><h3 className="rehab-team-card__name">{ m.name }</h3><span className="rehab-team-card__arrow">↗</span></div>
										<p className="rehab-team-card__role">{ m.role }</p>
										<p className="rehab-team-card__excerpt">{ m.excerpt }</p>
									</div>
								) ) }
							</div>
						</div>
					</section>
				</div>
			</>
		);
	},
	save: () => null,
} );
