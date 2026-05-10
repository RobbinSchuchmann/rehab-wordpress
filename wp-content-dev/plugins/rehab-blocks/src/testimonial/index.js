import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';
import metadata from './block.json';

const Star = ( { filled } ) => (
	<svg width="16" height="16" viewBox="0 0 24 24" fill={ filled ? 'currentColor' : 'none' } stroke="currentColor" strokeWidth="1.5" aria-hidden="true">
		<polygon points="12,2 15.1,8.6 22,9.5 17,14.4 18.2,21.5 12,18 5.8,21.5 7,14.4 2,9.5 8.9,8.6" />
	</svg>
);

const renderStars = ( rating ) => {
	const filled = Math.max( 0, Math.min( 5, rating || 0 ) );
	return (
		<div className="rehab-testimonial__stars" aria-label={ `${ filled } out of 5 stars` }>
			{ [ 0, 1, 2, 3, 4 ].map( ( i ) => (
				<Star key={ i } filled={ i < filled } />
			) ) }
		</div>
	);
};

function Edit( { attributes, setAttributes } ) {
	const { quote, name, role, rating } = attributes;
	const blockProps = useBlockProps( { className: 'rehab-testimonial' } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Testimonial', 'rehab-blocks' ) } initialOpen>
					<RangeControl
						label={ __( 'Star rating', 'rehab-blocks' ) }
						value={ rating }
						min={ 0 }
						max={ 5 }
						step={ 1 }
						onChange={ ( v ) => setAttributes( { rating: v } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				{ rating > 0 && renderStars( rating ) }
				<RichText
					tagName="p"
					className="rehab-testimonial__quote"
					value={ quote }
					onChange={ ( v ) => setAttributes( { quote: v } ) }
					placeholder={ __( 'Quote text…', 'rehab-blocks' ) }
					allowedFormats={ [ 'core/bold', 'core/italic' ] }
				/>
				<div className="rehab-testimonial__author">
					<RichText
						tagName="p"
						className="rehab-testimonial__name"
						value={ name }
						onChange={ ( v ) => setAttributes( { name: v } ) }
						placeholder={ __( 'Name', 'rehab-blocks' ) }
						allowedFormats={ [] }
					/>
					<RichText
						tagName="p"
						className="rehab-testimonial__role"
						value={ role }
						onChange={ ( v ) => setAttributes( { role: v } ) }
						placeholder={ __( 'Role / context', 'rehab-blocks' ) }
						allowedFormats={ [] }
					/>
				</div>
			</div>
		</>
	);
}

function save( { attributes } ) {
	const { quote, name, role, rating } = attributes;
	const blockProps = useBlockProps.save( { className: 'rehab-testimonial' } );
	return (
		<div { ...blockProps }>
			{ rating > 0 && renderStars( rating ) }
			<RichText.Content
				tagName="p"
				className="rehab-testimonial__quote"
				value={ quote }
			/>
			<div className="rehab-testimonial__author">
				<RichText.Content
					tagName="p"
					className="rehab-testimonial__name"
					value={ name }
				/>
				<RichText.Content
					tagName="p"
					className="rehab-testimonial__role"
					value={ role }
				/>
			</div>
		</div>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
